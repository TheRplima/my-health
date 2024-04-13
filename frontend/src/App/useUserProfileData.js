import { useState } from 'react'


const useUserProfileData = () => {
  const getUserProfileData = () => {
    const userProfileDataString = sessionStorage.getItem('userprofiledata')
    const userProfileData = JSON.parse(userProfileDataString)
    return userProfileData
  }

  const [userProfileData, setUserProfileData] = useState(getUserProfileData())

  const saveUserProfileData = (userProfileData) => {
    if (userProfileData !== null && userProfileData !== undefined) {
      sessionStorage.setItem('userprofiledata', JSON.stringify(userProfileData.user))
      setUserProfileData(userProfileData.user)
    } else {
      sessionStorage.removeItem('userprofiledata')
      setUserProfileData(null)
    }
  }
  return {
    getUserProfileData: getUserProfileData,
    setUserProfileData: saveUserProfileData,
    userProfileData
  }
}

export default useUserProfileData