import Form from 'react-bootstrap/Form';

const MultiSelectList = ({data, handleSelectedListItems, side}) => {

    return (
        <Form.Group>
        <Form.Control as="select" onChange={(e) => { handleSelectedListItems(e) }} multiple>
          {data.map(({ id, title, titleLoc }) => (
            titleLoc === side
              ? <option key={id} value={id}>{title}</option>
              : null
          ))}
        </Form.Control>
      </Form.Group>
    )
}

export default MultiSelectList