import { useState } from 'react'
import './App.scss'

function App() {
  const [count, setCount] = useState(0)

  return (
    <div className="App">
      <div className="left"></div>
      <div className="buttons">
        <button className="all-to-right" variant="primary">{">>"}</button>
        <button className="single-to-right">{">"}</button>
        <button className="single-to-left">{"<"}</button>
        <button className="all-to-left">{"<<"}</button>
      </div>
      <div className="right"></div>
    </div>
  )
}

export default App
