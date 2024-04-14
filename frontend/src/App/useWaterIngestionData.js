import { useState } from 'react'

async function getWaterIngestion(token) {
  return fetch('http://localhost:8000/api/water-ingestion/get-water-ingestion-by-day', {
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

const useWaterIngestionData = () => {
  const handleGetWaterIngestion = async (e) => {
    const tokenString = sessionStorage.getItem('token')
    if (tokenString !== null && tokenString !== undefined) {
      const token = JSON.parse(tokenString)
      const ret = await getWaterIngestion(token.token)

      sessionStorage.setItem('water_ingestion_list', JSON.stringify(ret.water_ingestion_list))
      sessionStorage.setItem('water_ingestion_total_amount', JSON.stringify(ret.total_amount))
      setWaterIngestionData(ret.water_ingestion_list)
      setTotalWaterIngestion(ret.total_amount)
  
      return ret.water_ingestion_list
    }

    return []
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

  const saveWaterIngestionData = (newWaterIngestion) => {
    if (newWaterIngestion !== null && newWaterIngestion !== undefined) {
      waterIngestionData.push(newWaterIngestion)
      const newTotalWaterIngestion = parseInt(totalWaterIngestion) + parseInt(newWaterIngestion.amount)
      sessionStorage.setItem('water_ingestion_list', JSON.stringify(waterIngestionData))
      setWaterIngestionData(waterIngestionData)
      sessionStorage.setItem('water_ingestion_total_amount', newTotalWaterIngestion)
      setTotalWaterIngestion(newTotalWaterIngestion)
    }
  }
  
  return {
    getWaterIngestionData: getWaterIngestionData,
    setWaterIngestionData: saveWaterIngestionData,
    waterIngestionData,
    totalWaterIngestion
  }
}

export default useWaterIngestionData