import { useState } from 'react'

async function getWeightControl(token) {
  return fetch('http://localhost:8000/api/weight-control', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + token,
    }
  }).then(data => data.json()).catch((error) => {
    console.log('Error', error.message);
  });
}

const useWeightControlData = () => {
  const handleGetWeightControl = async (e) => {
    const tokenString = sessionStorage.getItem('token')
    if (tokenString !== null && tokenString !== undefined) {
      const token = JSON.parse(tokenString)
      const ret = await getWeightControl(token.token)

      sessionStorage.setItem('weight_control_list', JSON.stringify(ret.weight_control_list))
      setWeightControlData(ret.weight_control_list)
  
      return ret.weight_control_list
    }

    return []
  }

  const getWeightControlData = () => {
    const weightControlDataString = sessionStorage.getItem('weight_control_list')

    if (weightControlDataString !== null && weightControlDataString !== undefined) {
      const weightControlData = JSON.parse(weightControlDataString)

      return weightControlData
    }

    return handleGetWeightControl()
  }

  const [weightControlData, setWeightControlData] = useState(getWeightControlData())

  const saveWeightControlData = (newWeightControl) => {
    if (newWeightControl !== null && newWeightControl !== undefined) {
      weightControlData.push(newWeightControl)
      sessionStorage.setItem('weight_control_list', JSON.stringify(weightControlData))
      setWeightControlData(weightControlData)
    }
  }
  
  return {
    getWeightControlData: getWeightControlData,
    setWeightControlData: saveWeightControlData,
    weightControlData
  }
}

export default useWeightControlData