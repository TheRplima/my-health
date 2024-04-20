import axios from 'axios';

const api = axios.create({
    baseURL: process.env.REACT_APP_API_BASE_URL, //your api URL
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
    }
});

export default api;