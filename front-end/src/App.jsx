import { useEffect, useState } from 'react'
import Form from 'react-bootstrap/Form';
import './App.scss';
import { AppService } from './services/app.service';

function App() {
  const [data, setData] = useState([])
  const [leftListSelectedItems, setLeftListSelectedItems] = useState([])
  const [RightListSelectedItems, setRightListSelectedItems] = useState([])

  const appService = new AppService();

  useEffect(() => {
    appService.getPosts().then(data => setData(data));
  }, [])


  // Events

  const handleSelectedItems = (e) => {
    setLeftListSelectedItems([...e.target.selectedOptions].map(o => o.value)); 
    // console.log(leftListSelectedItems)
  }

  const toRight = (id) => {
    appService.moveTo(id, "right")
  }

  const allToRight = () => {

    data.map(async ({id, titleLoc}) => {
      if (titleLoc === "left") {
        await appService.moveTo(id, "right")
      }
    })

    appService.getPosts().then(data => setData(data));
  }

  const allToLeft = async() => {

     await data.map(async ({id, titleLoc}) => {
      if (titleLoc === "right") {
        await appService.moveTo(id, "left")
      }
    })

    console.log("not finished")
    await appService.getPosts().then(data => setData(data));
  }

  // for testing
  const showData = () => {
    console.log(data)
  }

  return (
    <div className="App">
      <Form.Group>
        <Form.Control as="select" onChange={(e) => {handleSelectedItems(e)}} multiple>
          {data.map(({id, title, titleLoc}) => (
            titleLoc === "left"
            ? <option key={id} value={title}>{title}</option>
            : null
          ))}
        </Form.Control>
      </Form.Group>
      <div className="buttons">
        <button className="all-to-right" onClick={() => { allToRight() }}>{">>"}</button>
        <button className="single-to-right" onClick={() => {toRight(4)}}>{">"}</button>
        <button className="single-to-left">{"<"}</button>
        <button className="all-to-left" onClick={() => {allToLeft()}}>{"<<"}</button>
      </div>
      <Form.Group>
        <Form.Control as="select" onChange={(e) => {handleSelectedItems(e)}} multiple>
          {data.map(({id, title, titleLoc}) => (
            titleLoc === "right"
            ? <option key={id} value={title}>{title}</option>
            : null
          ))}
        </Form.Control>
      </Form.Group>
    </div>
  )
}

export default App
