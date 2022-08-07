import { useEffect, useState, useRef } from 'react'
import './App.scss';
import { AppService } from './services/app.service';
import MultiSelectList from './components/MultiSelectList';

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
  }

  const handleSelectedRightListItems = (e) => {
    setRightListSelectedItems([...e.target.selectedOptions].map(o => o.value));
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
      <h3>To select list item: ctrl + click (on windows)</h3>
      <h3>Command + click (on Mac)</h3>
      <div className="multi-list">
        <MultiSelectList data={data} handleSelectedListItems={handleSelectedLeftListItems} side="left"/>
        <div className="buttons">
          <button className="all-to-right" onClick={() => { allToRight() }}>{">>"}</button>
          <button className="single-to-right" onClick={() => { toRight() }}>{">"}</button>
          <button className="single-to-left" onClick={() => { toLeft() }}>{"<"}</button>
          <button className="all-to-left" onClick={() => { allToLeft() }}>{"<<"}</button>
        </div>
        <MultiSelectList data={data} handleSelectedListItems={handleSelectedRightListItems} side="right"/>
      </div>
    </div>
  )
}

export default App
