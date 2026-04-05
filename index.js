const express = require('express');
const app = express();

app.use(express.json());

// 📚 Book database (in-memory)
let books = [];

// ✅ GET all books
app.get('/books', (req, res) => {
    res.json({
        message: "List of all books",
        data: books
    });
});

// ✅ POST add new book
app.post('/books', (req, res) => {
    const { id, name, author } = req.body;

    // validation
    if (!id || !name || !author) {
        return res.status(400).json({
            message: "Please provide id, name and author"
        });
    }

    const newBook = { id, name, author };
    books.push(newBook);

    res.status(201).json({
        message: "Book added successfully",
        book: newBook
    });
});

// ✅ GET book by ID (extra for marks)
app.get('/books/:id', (req, res) => {
    const book = books.find(b => b.id == req.params.id);

    if (!book) {
        return res.status(404).json({ message: "Book not found" });
    }

    res.json(book);
});

// 🚀 Start server
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});