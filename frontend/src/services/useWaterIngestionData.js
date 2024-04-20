import { useState } from 'react'
import { useCookies } from 'react-cookie';
import api from './api';

const apiPrivate = (token) => {
  api.defaults.headers.Authorization = `Bearer ${token}`;
  return api
}

async function getWaterIngestion(token) {
  apiPrivate(token);

  return api.get('api/water-ingestion/get-water-ingestion-by-day').then(response => {
    return response.data
  }).catch(error => {
    console.log('Error', error.message);
  });
}

async function registerWaterIngestion(amount, token) {
  apiPrivate(token);

  return api.post('api/water-ingestion',{amount}).then(response => {
    return response.data
  }).catch(error => {
    console.log('Error', error.message);
  });
}

async function deleteWaterIngestion(id, token) {
  apiPrivate(token);

  return api.delete(`api/water-ingestion/${id}`).then(response => {
    return response.data
  }).catch(error => {
    console.log('Error', error.message);
  });
}

const useWaterIngestionData = () => {
  const [cookies, setCookies] = useCookies();

  const handleGetWaterIngestion = async (refresh = false) => {

    if (cookies.water_ingestions && refresh === false) {
      return cookies.water_ingestions
    }

    const token = cookies.token

    getWaterIngestion(token).then(data => {
      const water_ingestions = {
        list: data.water_ingestion_list,
        total_amount: data.total_amount
      }
      setCookies('water_ingestions', JSON.stringify(water_ingestions));

      return water_ingestions
    }).catch((error) => {
      console.log('Error', error.message);
    });

    return {}

  }

  const [waterIngestionData] = useState(handleGetWaterIngestion())

  const handleRegisterWaterIngestion = async (amount) => {
    const token = cookies.token

    registerWaterIngestion(amount, token).then(data => {
      handleGetWaterIngestion(true)
    }).catch((error) => {
      console.log('Error', error.message);
    });
  }

  const handleDeleteWaterIngestion = async (id) => {
    const token = cookies.token

    deleteWaterIngestion(id, token).then(data => {
      handleGetWaterIngestion(true)
    }).catch((error) => {
      console.log('Error', error.message);
    });
  }

  return {
    getWaterIngestionData: handleGetWaterIngestion,
    setWaterIngestionData: handleRegisterWaterIngestion,
    deleteWaterIngestion: handleDeleteWaterIngestion,
    waterIngestionData
  }
}

export default useWaterIngestionData