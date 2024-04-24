import { useState } from 'react'
import { useCookies } from 'react-cookie';
import api from './api';

const apiPrivate = (token) => {
  api.defaults.headers.Authorization = `Bearer ${token}`;
  return api
}

async function getWaterIntakeContainer(token) {
  apiPrivate(token);

  return api.get('api/water-intake-containers').then(response => {
    return response.data
  }).catch(error => {
    console.log('Error', error.message);
  });
}

async function registerWaterIntakeContainer(data, token) {
  apiPrivate(token);

  return api.post('api/water-intake-container',data).then(response => {
     
    return response.data
  }).catch(error => {
    console.log('Error', error.message);
  });
}

async function deleteWaterIntakeContainer(id, token) {
  apiPrivate(token);

  return api.delete(`api/water-intake-container/${id}`).then(response => {
    return response.data
  }).catch(error => {
    console.log('Error', error.message);
  });
}

const useWaterIntakeContainerData = () => {
  const [cookies, setCookies] = useCookies();

  const handleGetWaterIntakeContainer = async (refresh = false) => {

    if (cookies.water_intake_containers && refresh === false) {
      return await cookies.water_intake_containers
    }

    const token = cookies.token

    getWaterIntakeContainer(token).then(data => {

      setCookies('water_intake_containers', JSON.stringify(data.water_intake_container_list));

      return data.water_intake_container_list
    }).catch((error) => {
      console.log('Error', error.message);
    });

    return {}

  }

  const [waterIntakeContainerData] = useState(handleGetWaterIntakeContainer())

  const handleRegisterWaterIntakeContainer = async (data) => {
    const token = cookies.token

    registerWaterIntakeContainer(data, token).then(data => {
      handleGetWaterIntakeContainer(true)
    }).catch((error) => {
      console.log('Error', error.message);
    });
  }

  const handleDeleteWaterIntakeContainer = async (id) => {
    const token = cookies.token

    deleteWaterIntakeContainer(id, token).then(data => {
      handleGetWaterIntakeContainer(true)
    }).catch((error) => {
      console.log('Error', error.message);
    });
  }

  return {
    getWaterIntakeContainerData: handleGetWaterIntakeContainer,
    setWaterIntakeContainerData: handleRegisterWaterIntakeContainer,
    deleteWaterIntakeContainer: handleDeleteWaterIntakeContainer,
    waterIntakeContainerData
  }
}

export default useWaterIntakeContainerData