import { useEffect, useState, useRef } from 'react'
import Form from 'react-bootstrap/Form';
import './App.scss';
import { AppService } from './services/app.service';

function App() {
  const [data, setData] = useState([])
  const [buttonClicked, setButtonClicked] = useState(false);
  const mounted = useRef(true);

  const [leftListSelectedItems, setLeftListSelectedItems] = useState([]);
  const [rightListSelectedItems, setRightListSelectedItems] = useState([]);

  const appService = new AppService();

  useEffect(() => {

    mounted.current = true;

    if (data.length === 0 && mounted.current) {
      appService.getPosts().then(data => setData(data));
    }

    if (buttonClicked) {
      appService.getPosts().then(data => setData(data));
      setButtonClicked(false)
    }

    return () => mounted.current = false
  }, [buttonClicked, data])


  // Events

  const handleSelectedLeftListItems = (e) => {
    setLeftListSelectedItems([...e.target.selectedOptions].map(o => o.value));
    console.log(leftListSelectedItems)
  }

  const handleSelectedRightListItems = (e) => {
    setRightListSelectedItems([...e.target.selectedOptions].map(o => o.value));
    console.log(rightListSelectedItems)
  }

  const toRight = async () => {

    await leftListSelectedItems.map(id => {
      appService.moveTo(id, "right")
    })

    await setButtonClicked(true)
    await appService.getPosts().then(data => setData(data));
  }

  const toLeft = async () => {

    await rightListSelectedItems.map(id => {
      appService.moveTo(id, "left")
    })

    await setButtonClicked(true)
    await appService.getPosts().then(data => setData(data));
  }

  const allToRight = async () => {

    await data.map(({ id, titleLoc }) => {
      if (titleLoc === "left") {
         appService.moveTo(id, "right")
      }
    })

    await setButtonClicked(true)
    await appService.getPosts().then(data => setData(data));
  }

  const allToLeft = async () => {

     data.map(({ id, titleLoc }) => {
      if (titleLoc === "right") {
         appService.moveTo(id, "left")
      }
    })

    await setButtonClicked(true)
    await appService.getPosts().then(data => setData(data));
  }

  return (
    <div className="App">
      <Form.Group>
        <Form.Control as="select" onChange={(e) => { handleSelectedLeftListItems(e) }} multiple>
          {data.map(({ id, title, titleLoc }) => (
            titleLoc === "left"
              ? <option key={id} value={id}>{title}</option>
              : null
          ))}
        </Form.Control>
      </Form.Group>
      <div className="buttons">
        <button className="all-to-right" onClick={() => { allToRight() }}>{">>"}</button>
        <button className="single-to-right" onClick={() => { toRight() }}>{">"}</button>
        <button className="single-to-left" onClick={() => { toLeft() }}>{"<"}</button>
        <button className="all-to-left" onClick={() => { allToLeft() }}>{"<<"}</button>
      </div>
      <Form.Group>
        <Form.Control as="select" onChange={(e) => { handleSelectedRightListItems(e) }} multiple>
          {data.map(({ id, title, titleLoc }) => (
            titleLoc === "right"
              ? <option key={id} value={id}>{title}</option>
              : null
          ))}
        </Form.Control>
      </Form.Group>
    </div>
  )
}

export default App
