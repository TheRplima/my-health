import React, { useState } from 'react'


const useUserProfileData = () => {
  const getUserProfileData = () => {
    const userProfileDataString = sessionStorage.getItem('userprofiledata')
    const userProfileData = JSON.parse(userProfileDataString)
    return userProfileData
  }
  const WaterIngestedTodayData = () => {
    const userProfileDataString = sessionStorage.getItem('userprofiledata')
    const userProfileData = JSON.parse(userProfileDataString)
    return userProfileData.water_ingestion_today_amount
  }

  const [userProfileData, setUserProfileData] = useState(getUserProfileData())
  const [waterIngestedToday, setWaterIngestedToday] = useState(WaterIngestedTodayData())

  const saveUserProfileData = (userProfileData,newWaterIngestion = null) => {

    if (userProfileData !== null && userProfileData !== undefined){
      if (newWaterIngestion !== null && newWaterIngestion !== undefined){
        userProfileData.water_ingestion_today.push(newWaterIngestion)
        userProfileData.water_ingestion_today_amount = parseInt(userProfileData.water_ingestion_today_amount) + parseInt(newWaterIngestion.amount)
        sessionStorage.setItem('userprofiledata', JSON.stringify(userProfileData))
        setUserProfileData(userProfileData)
        setWaterIngestedToday(userProfileData.water_ingestion_today_amount)
      }else{
        userProfileData.user.water_ingestion_today_amount = userProfileData.waterIngestionsTodayAmount
        sessionStorage.setItem('userprofiledata', JSON.stringify(userProfileData.user))
        setUserProfileData(userProfileData.user)
        setWaterIngestedToday(userProfileData.user.water_ingestions_today_amount)
      }
    }else{
      sessionStorage.removeItem('userprofiledata')
      setUserProfileData(null)
    }
  }
  return {
    getUserProfileData: getUserProfileData,
    setUserProfileData: saveUserProfileData,
    userProfileData,
    waterIngestedToday
  }
}

export default useUserProfileData