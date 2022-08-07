export class AppService {


    async getPosts() {
        const response = await fetch('https://127.0.0.1:8000/posts');
        return await response.json();
    }

    async moveTo(id, side) {

        const title = await fetch('https://127.0.0.1:8000/posts/' + id)
        const objeTitle = await title.json();
         objeTitle.titleLoc = side

        const response = await fetch('https://127.0.0.1:8000/posts/' + id, {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(objeTitle)
          })

          let test = await response.json()
        return await response.json();
    }

    // async addUser(user) {
    //     const response = await fetch(`/api/user`, {
    //         method: 'POST',
    //         headers: {'Content-Type': 'application/json'},
    //         body: JSON.stringify({user})
    //       })
    //     return await response.json();
    // }

}