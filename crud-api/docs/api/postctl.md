# Producing RESTful API
 Follow  the REST convention, we are going to create the following APIs.

* `GET  /posts`    Get all posts.
* `GET /posts/{id}` Get a single post by ID, if not found, return status 404 
* `POST /posts` Create a new post from  request body, add the new post URI to response header `Location`, and return status 201
* `DELETE /posts/{id}` Delete a single post by ID, return status 204. If the post was  not found, return status 404 instead.
* ...

Next let's create a `Controller`  to handle the  incoming requests.


## Creating PostController

To create a `Controller` skeleton, run the following command and follow the interactive guide to create a controller named `PostController`.

```bash
# php bin/console make:constroller
```

Open *src/Controller/PostController.php* in IDE.

Add a new function to retrieve all posts.  To bind the request path to the controller,  add a  `Route` attribute on class level and the `all` function. The former `Route` will apply to all functions in this controller.

```bash
#[Route(path: "/posts", name: "posts_")]
class PostController extends AbstractController
{
    public function __construct(private PostRepository      $posts)
    {
    }

    #[Route(path: "", name: "all", methods: ["GET"])]
    function all(): Response
    {
        $data = $this->posts->findAll();
        return $this->json($data);
    }
    
}
```

Start up the application, and try to access the *http://localhost:8000/posts*, it will throw a circular dependencies exception when rendering the models in JSON view directly. 

There are some solutions to avoid this, the simplest is break the bi-direction relations before rendering the JSON view.  

Add a `Ignore` attribute on `Comment.post` and `Tag.posts`.

```php
//src/Entity/Comment.php
class Comment
{
    #[Ignore]
    private Post $post;
}

//src/Entity/Tag.php
class Tag
{
    #[Ignore]
    private Collection $posts;
}
```

> Alternatively,  the DTO pattern is a good option to transform the data to a plain object that only includes *essential* fields before rendering in the HTTP response.

### Paginating Result

There are a lot of web applications which provide a input field for typing keyword and paginating the search results. Assume there is a *keyword* provided by request to match Post *title* or *content* fields, a  *offset* to set the offset position of the pagination, and a *limit* to set the limited size of the elements per page. Create  a function in the `PostRepository`, accepts a *keyword*, *offset* and *limit* as arguments.

```php
public function findByKeyword(string $q, int $offset = 0, int $limit = 20): Page
{
    $query = $this->createQueryBuilder("p")
        ->andWhere("p.title like :q or p.content like :q")
        ->setParameter('q', "%" . $q . "%")
        ->orderBy('p.createdAt', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult($offset)
        ->getQuery();

    $paginator = new Paginator($query, $fetchJoinCollection = false);
    $c = count($paginator);
    $content = new ArrayCollection();
    foreach ($paginator as $post) {
        $content->add(PostSummaryDto::of($post->getId(), $post->getTitle()));
    }
    return Page::of ($content, $c, $offset, $limit);
}
```

Firstly, create a dynamic query using `createQueryBuilder` ,  then create a Doctrine `Paginator` instance to execute the query. The `Paginator` implements `Countable` interface,  use `count` to get the count of total elements. Finally, we use a custom `Page` object to wrap the result.

```php
class Page
{
    private Collection $content;
    private int $totalElements;
    private int $offset;
    private int $limit;

    #[Pure] public function __construct()
    {
        $this->content = new ArrayCollection();
    }


    public static function of(Collection $content, int $totalElements, int $offset = 0, int $limit = 20): Page
    {
        $page = new Page();
        $page->setContent($content)
            ->setTotalElements($totalElements)
            ->setOffset($offset)
            ->setLimit($limit);

        return $page;
    }
    
    //
    //getters

}    
```

### Handling Query Parameters

In the `PostController` , let's improve the the function which serves the route `/posts`, make it accept query parameters like */posts?q=Symfony&offset=0&limit=10*, and ensure the parameters are optional.

```php
    #[Route(path: "", name: "all", methods: ["GET"])]
    function all(Request $req): Response
    {
        $keyword = $req->query->get('q')??'';
        $offset = $req->query->get('offset')??0;
        $limit = $req->query->get('limit')??10;
        
        $data = $this->posts->findByKeyword($keyword, $offset, $limit);
        return $this->json($data);
    }
```

It works but the query parameters handling looks a little ugly.  It is great if they can be handled as the route path parameters.  

We can create a custom `ArgumentResolver` to resolve the bound query arguments.

Firstly create an Annotation/Attribute class to identify a query parameter that need to be resolved by this `ArgumentResolver`.

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final class QueryParam
{
    private null|string $name;
    private bool $required;

    /**
     * @param string|null $name
     * @param bool $required
     */
    public function __construct(?string $name = null, bool $required = false)
    {
        $this->name = $name;
        $this->required = $required;
    }
    
    //getters and setters
    
}    
```

Create a custom `ArgumentResolver` implements the built-in `ArgugmentResolverInterface`.

```php
class QueryParamValueResolver implements ArgumentValueResolverInterface, LoggerAwareInterface
{
    public function __construct()
    {
    }

    private LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $argumentName = $argument->getName();
        $this->logger->info("Found [QueryParam] annotation/attribute on argument '" . $argumentName . "', applying [QueryParamValueResolver]");
        $type = $argument->getType();
        $nullable = $argument->isNullable();
        $this->logger->debug("The method argument type: '" . $type . "' and nullable: '" . $nullable . "'");

        //read name property from QueryParam
        $attr = $argument->getAttributes(QueryParam::class)[0];// `QueryParam` is not repeatable
        $this->logger->debug("QueryParam:" . $attr);
        //if name property is not set in `QueryParam`, use the argument name instead.
        $name = $attr->getName() ?? $argumentName;
        $required = $attr->isRequired() ?? false;
        $this->logger->debug("Polished QueryParam values: name='" . $name . "', required='" . $required . "'");

        //fetch query name from request
        $value = $request->query->get($name);
        $this->logger->debug("The request query parameter value: '" . $value . "'");

        //if default value is set and query param value is not set, use default value instead.
        if (!$value && $argument->hasDefaultValue()) {
            $value = $argument->getDefaultValue();
            $this->logger->debug("After set default value: '" . $value . "'");
        }

        if ($required && !$value) {
            throw new \InvalidArgumentException("Request query parameter '" . $name . "' is required, but not set.");
        }

        $this->logger->debug("final resolved value: '" . $value . "'");
        
        //must return  a `yield` clause
        yield match ($type) {
            'int' => $value ? (int)$value : 0,
            'float' => $value ? (float)$value : .0,
            'bool' => (bool)$value,
            'string' => $value ? (string)$value : ($nullable ? null : ''),
            null => null
        };
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $attrs = $argument->getAttributes(QueryParam::class);
        return count($attrs) > 0;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
```

 At runtime, it calls the `supports` function to check it the current request satisfy the requirement, if it is ok, then invoke the `resovle` funtion.

In the `supports` function, we check if the argument is annotated with a `QueryParam`, if it is existed, then resolved the argument from request query string. 

Now change the function that serves */posts* endpoint to the following.

```php
#[Route(path: "", name: "all", methods: ["GET"])]
function all(#[QueryParam] $keyword,
    #[QueryParam] int $offset = 0,
    #[QueryParam] int $limit = 20): Response
    {
        $data = $this->posts->findByKeyword($keyword || '', $offset, $limit);
        return $this->json($data);
    }
```

Run the application and test the */posts* using `curl`.

```bash
# curl http://localhost:8000/posts
{
    "content":[
    	{
            "id":"1ec3e1e0-17b3-6ed2-a01c-edecc112b436",
            "title":"Building Restful APIs with Symfony and PHP 8"
        }
    ],
    "totalElements":1,
    "offset":0,
    "limit":20
}
```



##  Retrieving Post

Follow the design in the previous section, add another function to `PostController` to map route `/posts/{id}` . 

```bash
class PostController extends AbstractController
{
	//other functions...

    #[Route(path: "/{id}", name: "byId", methods: ["GET"])]
    function getById(Uuid $id): Response
    {
        $data = $this->posts->findOneBy(["id" => $id]);
        if ($data) {
            return $this->json($data);
        } else {
            return $this->json(["error" => "Post was not found by id:" . $id], 404);
        }
    }
}
```

Run the application, and try to access *http://localhost:8000/posts/{id}*, it will throw an exception like this.

```bash
App\Controller\PostController::getById(): Argument #1 ($id) must be of type Symfony\Component\Uid\Uuid, string given, cal
led in D:\hantsylabs\symfony5-sample\rest-sample\vendor\symfony\http-kernel\HttpKernel.php on line 156

```

The `id` in the URI is a string,  can not be  used  as `Uuid`  directly.

Symfony provides `ParamConverter` to convert the request attributes to the target type. We can create a custom `ParamConverter` to archive the purpose.

### Converting Request Attributes 

Create a  new class `UuidParamCovnerter` under *src/Request/* folder.

```php
class UuidParamConverter implements ParamConverterInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }


    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {

        $param = $configuration->getName();

        if (!$request->attributes->has($param)) {
            return false;
        }

        $value = $request->attributes->get($param);
        $this->logger->info("parameter value:" . $value);
        if (!$value && $configuration->isOptional()) {
            $request->attributes->set($param, null);

            return true;
        }

        $data = Uuid::fromString($value);
        $request->attributes->set($param, $data);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        $className = $configuration->getClass();
        $this->logger->info("converting to UUID :{c}", ["c" => $className]);
        return $className && $className == Uuid::class;
    }
}
```



In the above codes, 

* The `supports` function to check the execution environment if matching the requirements

* The `apply` function to perform the conversion. if `supports` returns false, this conversion step will be skipped.

  

## Creating Post

Follow the REST convention, define the following rule to serve an endpoint to handle the request.

* Request matches Http verbs/HTTP Method: `POST`
* Request matches route endpoint: */posts*
* Set request header  `Content-Type` value to *application/json*, and use request body to hold request data as JSON format
* If successful, return a `CREATED`(201) Http Status code, and set the response header *Location* value to the URI of the new created post.

```php
#[Route(path: "", name: "create", methods: ["POST"])]
public function create(Request $request): Response
{
    $data = $this->serializer->deserialize($request->getContent(), CreatePostDto::class, 'json');
    $entity = PostFactory::create($data->getTitle(), $data->getContent());
    $this->posts->getEntityManager()->persist($entity);

    return $this->json([], 201, ["Location" => "/posts/" . $entity->getId()]);
}
```

The `posts->getEntityManager()` overrides parent methods to get a `EntityManager` from parent class, you can also inject `ObjectManager` or `EntityManagerInterface` in the  `PostController` directly to do the persistence work. The Doctrine `Repository` is mainly designated to build query criteria and execute custom queries.

### Converting Request Body 

We can also use an Annotation/Attribute to erase the raw codes of handling `Request` object through introducing a custom  `ArgumentResolver`.

Create a `Body` *Attribute*.

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final class Body
{
}
```

Then create a `BodyValueResolver`.

```php
class BodyValueResolver implements ArgumentValueResolverInterface, LoggerAwareInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    private LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $type = $argument->getType();
        $this->logger->debug("The argument type:'" . $type . "'");
        $format = $request->getContentType() ?? 'json';
        $this->logger->debug("The request format:'" . $format . "'");

        //read request body
        $content = $request->getContent();
        $data = $this->serializer->deserialize($content, $type, $format);
       // $this->logger->debug("deserialized data:{0}", [$data]);
        yield $data;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $attrs = $argument->getAttributes(Body::class);
        return count($attrs) > 0;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

```

In the `supports` method, it simply detects if the method argument annotated with a `Body` attribute, then apply `resolve` method to deserialize the request body  content to a typed object.

Run the application and test the endpoint through */posts*.

```php
curl -v http://localhost:8000/posts -H "Content-Type:application/json" -d "{\"title\":\"test title\",\"content\":\"test content\"}"
> POST /posts HTTP/1.1
> Host: localhost:8000
> User-Agent: curl/7.55.1
> Accept: */*
> Content-Type:application/json
> Content-Length: 47
>
< HTTP/1.1 201 Created
< Cache-Control: no-cache, private
< Content-Type: application/json
< Date: Sun, 21 Nov 2021 08:42:49 GMT
< Location: /posts/1ec4aa70-1b21-6bce-93f8-b39330fe328e
< X-Powered-By: PHP/8.0.10
< X-Robots-Tag: noindex
< Content-Length: 2
<
[]
```

### Validating Request

In the last section,  we convert the request body into an plain object. To validate the object, generally we can inject a `Validator` service. 

```php
__constructor(ValidatorInterface $validator, ...){}
```

Then invoke `validate` function to validate the target value, store the validation result into an  `errors` object,  you can process it later.

```php
$errors = $validator->validate($body);

if (count($errors) > 0) {
    //...
}
```

Like the above section, you can create  a custom  `ArgumentValueResolver`  and a specific `Attribute` to handle the validation automatically. 

But I hope the official *validation attributes* can be applied on the controller method arguments directly,  like the existing Bean Validation in a Spring Controller. For example, 

```php
#[Route(path: "", name: "all", methods: ["GET"])]
function all(string $keyword, #[PositiveOrZero] int $offset = 0, #[Positive] int $limit = 20): Response
{
    //...
}
             
#[Route(path: "", name: "all", methods: ["POST"])]
function create(#[Body] #[Valid] data: CreatePostCommand): Response
{
    //...
}             
```

Please vote [issue #43958](https://github.com/symfony/symfony/issues/43958) if you like include this feature.

## Updating Post

Follow the REST convention, define the following rule to serve an endpoint to handle the request.

* Request matches Http verbs/HTTP Method: `PUT`
* Request matches route endpoint: */posts/{id}*
* If successful, return a `NO_CONTENT`(204) Http Status code and an empty response body.

```php
#[Route(path: "/{id}", name: "update", methods: ["PUT"])]
public function update(Uuid $id, #[Body] UpdatePostDto $data): Response
{
    $entity = $this->posts->findOneBy(["id" => $id]);
    if (!$entity) {
        throw new PostNotFoundException($id);
        //return $this->json(["error" => "Post was not found by id:" . $id], 404);
    }
    $entity->setTitle($data->getTitle())
        ->setContent($data->getContent());
    $this->objectManager->merge($entity);
    $this->objectManager->flush();

    return $this->json([], 204);
}
```

Firstly we retrieve the existing post through the `id` path variable.   Update the existing  post with data from the request body, and save it back to the database.

### Updating Post Status 

In the above update operation, we do not update the status field. In a real world application, in some cases we could update a single field instead of the world entity. 

The status field of a `Post` can be updated via a standalone endpoint.

Follow the REST convention, define the following rule to serve an endpoint to handle the request.

* Request matches Http verbs/HTTP Method: `PUT`
* Request matches route endpoint: */posts/{id}/status*
* If successful, return a `NO_CONTENT`(204) Http Status code and an empty response body.

```php
#[Route(path: "/{id}/status", name: "update_status", methods: ["PUT"])]
public function updateStatus(Uuid $id, #[Body] UpdatePostStatusDto $data): Response
{
    $entity = $this->posts->findOneBy(["id" => $id]);
    if (!$entity) {
        throw new PostNotFoundException($id);
        //return $this->json(["error" => "Post was not found by id:" . $id], 404);
    }
    echo "update post status::::" . PHP_EOL;
    var_export($data);
    $entity->setStatus($data->getStatus());
    $this->objectManager->merge($entity);
    $this->objectManager->flush();

    return $this->json([], 204);
}
```

## Deleting Post

Follow the REST convention, define the following rule to serve an endpoint to handle the request.

* Request matches Http verbs/HTTP Method: `DELETE`
* Request matches route endpoint: */posts/{id}*
* If successful, return a `NO_CONTENT`(204) Http Status code and an empty response body.

```php
#[Route(path: "/{id}", name: "delete", methods: ["DELETE"])]
public function deleteById(Uuid $id): Response
{
    $entity = $this->posts->findOneBy(["id" => $id]);
    if (!$entity) {
        throw new PostNotFoundException($id);
        //return $this->json(["error" => "Post was not found by id:" . $id], 404);
    }
    $this->objectManager->remove($entity);
    $this->objectManager->flush();

    return $this->json([], 204);
}
```

