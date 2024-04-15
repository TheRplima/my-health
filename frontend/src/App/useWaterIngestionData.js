import { useState } from 'react'

async function getWaterIngestion(token) {
  return fetch(process.env.REACT_APP_API_BASE_URL + '/water-ingestion/get-water-ingestion-by-day', {
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

async function registerWaterIngestion(amount, token) {
  return fetch(process.env.REACT_APP_API_BASE_URL + '/water-ingestion', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + token,
    },
    body: JSON.stringify({ amount })
  }).then(data => data.json()).catch((error) => {
    console.log('Error', error.message);
  });
}

async function deleteWaterIngestion(id, token) {
  return fetch(process.env.REACT_APP_API_BASE_URL + `/water-ingestion/${id}`, {
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

const useWaterIngestionData = () => {
  const handleGetWaterIngestion = async (e) => {
    const tokenString = sessionStorage.getItem('token')
    if (tokenString === null || tokenString === undefined) {
      alert('Sessão expirada. Faça login novamente.')
      sessionStorage.clear();
      window.location.reload();
    }

    const token = JSON.parse(tokenString)
    let ret = []
    getWaterIngestion(token.token).then(data => {
      ret = data.water_ingestion_list
      sessionStorage.setItem('water_ingestion_list', JSON.stringify(data.water_ingestion_list))
      sessionStorage.setItem('water_ingestion_total_amount', JSON.stringify(data.total_amount))
      setWaterIngestionData(data.water_ingestion_list)
      setTotalWaterIngestion(data.total_amount)
    }).catch((error) => {
      console.log('Error', error.message);
    });

    return ret

  }

  const getWaterIngestionData = () => {
    const waterIngestionDataString = sessionStorage.getItem('water_ingestion_list')

    if (waterIngestionDataString !== null && waterIngestionDataString !== undefined) {
      const waterIngestionData = JSON.parse(waterIngestionDataString)

      return waterIngestionData
    }

    return handleGetWaterIngestion()
  }

  const getTotalWaterIngestion = () => {
    const totalWaterIngestion = sessionStorage.getItem('water_ingestion_total_amount')

    if (totalWaterIngestion !== null && totalWaterIngestion !== undefined) {
      return totalWaterIngestion
    }

    return handleGetWaterIngestion()
  }

  const [waterIngestionData, setWaterIngestionData] = useState(getWaterIngestionData())
  const [totalWaterIngestion, setTotalWaterIngestion] = useState(getTotalWaterIngestion())

  const handleRegisterWaterIngestion = async (amount) => {
    const tokenString = sessionStorage.getItem('token')
    if (tokenString === null || tokenString === undefined) {
      alert('Sessão expirada. Faça login novamente.')
      sessionStorage.clear();
      window.location.reload();
    }

    const token = JSON.parse(tokenString)
    registerWaterIngestion(amount, token.token).then(data => {
      const newWaterIngestion = data.water_ingestion
      waterIngestionData.push(newWaterIngestion)
      const newTotalWaterIngestion = parseInt(totalWaterIngestion) + parseInt(newWaterIngestion.amount)
      sessionStorage.setItem('water_ingestion_list', JSON.stringify(waterIngestionData))
      setWaterIngestionData(waterIngestionData)
      sessionStorage.setItem('water_ingestion_total_amount', newTotalWaterIngestion)
      setTotalWaterIngestion(newTotalWaterIngestion)
    }).catch((error) => {
      console.log('Error', error.message);
    });
  }

  const handleDeleteWaterIngestion = async (id) => {
    const tokenString = sessionStorage.getItem('token')
    if (tokenString === null || tokenString === undefined) {
      alert('Sessão expirada. Faça login novamente.')
      sessionStorage.clear();
      window.location.reload();
    }

    const token = JSON.parse(tokenString)
    deleteWaterIngestion(id, token.token).then(data => {
      const deleltedWaterIngestion = data.water_ingestion
      const index = waterIngestionData.findIndex(waterIngestion => waterIngestion.id === deleltedWaterIngestion.id)
      waterIngestionData.splice(index, 1)
      const newTotalWaterIngestion = parseInt(totalWaterIngestion) - parseInt(deleltedWaterIngestion.amount)
      sessionStorage.setItem('water_ingestion_list', JSON.stringify(waterIngestionData))
      setWaterIngestionData(waterIngestionData)
      sessionStorage.setItem('water_ingestion_total_amount', newTotalWaterIngestion)
      setTotalWaterIngestion(newTotalWaterIngestion)
    }).catch((error) => {
      console.log('Error', error.message);
    });
  }

  return {
    getWaterIngestionData: getWaterIngestionData,
    setWaterIngestionData: handleRegisterWaterIngestion,
    deleteWaterIngestion: handleDeleteWaterIngestion,
    waterIngestionData,
    totalWaterIngestion
  }
}

export default useWaterIngestionData