import { useState } from 'react'
import useUserProfileData from './useUserProfileData';
import useToken from './useToken';

async function getWeightControl(token, max = 0, initial_date = null, final_date = null) {
  let queryString = '';
  if (initial_date !== null) {
    queryString += `?initial_date=${initial_date}`
  }
  if (final_date !== null) {
    queryString += queryString.length > 0 ? '&' : '?'
    queryString += `final_date=${final_date}`
  }
  if (max > 0) {
    queryString += queryString.length > 0 ? '&' : '?'
    queryString += `max=${max}`
  }

  return fetch(process.env.REACT_APP_API_BASE_URL + 'api/weight-control'+queryString, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + token,
    }
  }).then(data => data.json()).catch((error) => {
    console.log('Error', error.message);
  });
}

async function registerWeightControl(weight, token) {
  return fetch(process.env.REACT_APP_API_BASE_URL + 'api/weight-control', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + token,
    },
    body: JSON.stringify({ weight })
  }).then(data => data.json()).catch((error) => {
    console.log('Error', error.message);
  });
}

async function deleteWeightControl(id, token) {
  return fetch(process.env.REACT_APP_API_BASE_URL + `api/weight-control/${id}`, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + token,
    }
  }).then(data => data.json()).catch((error) => {
    console.log('Error', error.message);
  });
}

const useWeightControlData = (max = 0, initial_date = null, final_date = null) => {

  const { refreshUserData } = useUserProfileData()
  const { getToken } = useToken()

  const getWeightControlData = async (max = 0, initial_date = null, final_date = null) => {
    const token = getToken()

    getWeightControl(token, max, initial_date, final_date).then(data => {
      sessionStorage.setItem('weight_control_list', JSON.stringify(data.weight_control_list))
      setWeightControlData(data.weight_control_list)
      
      return data.weight_control_list
    }).catch((error) => {
      console.log('Error', error.message);

      return []
    });
  }

  const handleGetWeightControl = (max = 0, initial_date = null, final_date = null) => {
    const weightControlDataString = sessionStorage.getItem('weight_control_list')

    if (weightControlDataString !== null && weightControlDataString !== undefined && weightControlDataString !== 'undefined') {
      const weightControlData = JSON.parse(weightControlDataString)

      return weightControlData
    }

    return getWeightControlData(max, initial_date, final_date)
  }

  const [weightControlData, setWeightControlData] = useState(handleGetWeightControl(max, initial_date, final_date))


  const handleRegisterWeightControl = async (weight) => {
    const token = getToken()

    registerWeightControl(weight, token).then(data => {
      getWeightControlData(max, initial_date, final_date).then(data => {
        refreshUserData()
      })
    }).catch((error) => {
      console.log('Error', error.message);
    });
  }
  
  const handleDeleteWeightControl = async (id) => {
    const token = getToken()

    deleteWeightControl(id, token).then(data => {
      getWeightControlData(max, initial_date, final_date).then(data => {
        refreshUserData()
      })
    }).catch((error) => {
      console.log('Error', error.message);
    });
  }

  return {
    getWeightControlData: handleGetWeightControl,
    setWeightControlData: handleRegisterWeightControl,
    deleteWeightControl: handleDeleteWeightControl,
    weightControlData
  }
}

export default useWeightControlData