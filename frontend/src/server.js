const express = require('express')
const cors = require('cors')

const app = express()
app.use(cors())

app.use('/api/login', (req, res) => {
    res.send({
        token: 'test123'
    })
})

app.listen(8000, () => console.log('API is running on localhost:8000/api/login'))