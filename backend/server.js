const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');

const app = express();
app.use(express.json());
app.use(cors());

// 🔗 MongoDB Connection
mongoose.connect('mongodb+srv://mayankrane2005:admin123@cluster0.ohd5rem.mongodb.net/flightDB')
.then(() => console.log("MongoDB Connected"))
.catch(err => console.log(err));

// 📦 Schema
const flightSchema = new mongoose.Schema({
    name: String,
    from: String,
    to: String,
    phone: String,
    email: String
});

const Flight = mongoose.model('Flight', flightSchema);

// ➕ CREATE
app.post('/add', async (req, res) => {
    const data = new Flight(req.body);
    await data.save();
    res.send("Data Added");
});

// 📄 READ
app.get('/get', async (req, res) => {
    const data = await Flight.find();
    res.json(data);
});

// ❌ DELETE
app.delete('/delete/:id', async (req, res) => {
    await Flight.findByIdAndDelete(req.params.id);
    res.send("Deleted");
});

// ✏️ UPDATE
app.put('/update/:id', async (req, res) => {
    await Flight.findByIdAndUpdate(req.params.id, req.body);
    res.send("Updated");
});

// 🚀 Start server
app.listen(5000, () => {
    console.log("Server running on port 5000");
});