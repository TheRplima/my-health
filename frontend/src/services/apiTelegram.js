import axios from 'axios';

const apiTelegram = axios.create({
    
    baseURL: process.env.REACT_APP_API_TELEGRAM_BASE_URL+process.env.REACT_APP_API_TELEGRAM_BOT_ID+':'+process.env.REACT_APP_API_TELEGRAM_TOKEN+'/', //your api URL
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
    }
});

export default apiTelegram;