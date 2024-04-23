import { useState } from 'react'
import { useCookies } from 'react-cookie';
import api from './api';
import useUserProfileData from './useUserProfileData';

const apiPrivate = (token) => {
  api.defaults.headers.Authorization = `Bearer ${token}`;
  return api
}

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
  apiPrivate(token);
  return api.get('api/weight-control'+queryString).then(response => {
    return response.data
  }).catch(error => {
    console.log('Error', error.message);
  });
}

async function registerWeightControl(weight, token) {
  apiPrivate(token);

  return api.post('api/weight-control',{weight}).then(response => {
    return response.data
  }).catch(error => {
    console.log('Error', error.message);
  });
}

async function deleteWeightControl(id, token) {
  apiPrivate(token);

  return api.delete(`api/weight-control/${id}`).then(response => {
    return response.data
  }).catch(error => {
    console.log('Error', error.message);
  });
}

const useWeightControlData = (max = 0, initial_date = null, final_date = null) => {
  const [cookies, setCookies] = useCookies();
  const { getUserProfileData } = useUserProfileData()

  const handleGetWeightControl = async (refresh = false, max = 0, initial_date = null, final_date = null) => {

    if (cookies.weight_controls && refresh === false) {
      return cookies.weight_controls
    }

    const token = cookies.token

    getWeightControl(token, max, initial_date, final_date).then(data => {

      setCookies('weight_controls', JSON.stringify(data.weight_control_list));

      return data.weight_control_list
    }).catch((error) => {
      console.log('Error', error.message);
    });

    return {}

  }

  const [weightControlData] = useState(handleGetWeightControl(false, max, initial_date, final_date))

  const handleRegisterWeightControl = async (weight) => {
    const token = cookies.token

    registerWeightControl(weight, token).then(data => {
      handleGetWeightControl(true, max, initial_date, final_date)
      getUserProfileData(true);
    }).catch((error) => {
      console.log('Error', error.message);
    });
  }

  const handleDeleteWeightControl = async (id) => {
    const token = cookies.token

    deleteWeightControl(id, token).then(data => {
      handleGetWeightControl(true, max, initial_date, final_date)
      getUserProfileData(true);
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