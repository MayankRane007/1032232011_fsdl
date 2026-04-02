import React, { useState } from 'react';

function App() {
  // 1. State hooks to manage our data and UI
  const [formData, setFormData] = useState({ name: '', email: '' });
  const [errors, setErrors] = useState({});
  const [isSuccess, setIsSuccess] = useState(false);

  // 2. Function to update state when the user types
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
    
    // Clear the specific error when the user starts typing again
    setErrors({ ...errors, [name]: '' });
    setIsSuccess(false); 
  };

  // 3. Function triggered when the SUBMIT button is clicked
  const handleSubmit = (e) => {
    e.preventDefault(); 
    let newErrors = {};

    // Validation: Check if Name is empty
    if (!formData.name.trim()) {
      newErrors.name = "Name field cannot be empty";
    }

    // Validation: Check if Email is empty or incorrectly formatted
    if (!formData.email.trim()) {
      newErrors.email = "Email field cannot be empty";
    } else {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(formData.email)) {
        newErrors.email = "Wrong format of email ID"; 
      }
    }

    setErrors(newErrors);

    // If there are no errors in our newErrors object, submission is successful
    if (Object.keys(newErrors).length === 0) {
      setIsSuccess(true);
    } else {
      setIsSuccess(false);
    }
  };

  // --- STYLING OBJECTS ---
  const styles = {
    pageBackground: {
      minHeight: '100vh',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      fontFamily: '"Segoe UI", Roboto, Helvetica, Arial, sans-serif',
      padding: '20px'
    },
    card: {
      background: 'white',
      padding: '40px',
      borderRadius: '12px',
      boxShadow: '0 10px 25px rgba(0, 0, 0, 0.2)',
      width: '100%',
      maxWidth: '400px'
    },
    header: {
      textAlign: 'center',
      color: '#333',
      marginBottom: '24px',
      fontSize: '28px',
      fontWeight: 'bold'
    },
    label: {
      display: 'block',
      marginBottom: '8px',
      color: '#555',
      fontWeight: '500'
    },
    input: (hasError) => ({
      width: '100%',
      padding: '12px',
      marginBottom: '6px',
      borderRadius: '8px',
      border: hasError ? '2px solid #ff4d4f' : '1px solid #ddd',
      backgroundColor: hasError ? '#fff1f0' : '#f9f9f9',
      boxSizing: 'border-box',
      fontSize: '16px',
      outline: 'none',
      transition: 'all 0.3s ease'
    }),
    errorText: {
      color: '#ff4d4f',
      fontSize: '13px',
      fontWeight: '500',
      display: 'block',
      marginTop: '4px'
    },
    button: {
      width: '100%',
      padding: '14px',
      backgroundColor: '#667eea',
      color: 'white',
      border: 'none',
      borderRadius: '8px',
      cursor: 'pointer',
      fontSize: '16px',
      fontWeight: 'bold',
      marginTop: '10px',
      boxShadow: '0 4px 10px rgba(102, 126, 234, 0.4)',
      transition: 'background 0.3s ease'
    },
    successBox: {
      padding: '15px',
      backgroundColor: '#d4edda',
      color: '#155724',
      marginBottom: '20px',
      borderRadius: '8px',
      textAlign: 'center',
      fontWeight: '500',
      border: '1px solid #c3e6cb'
    }
  };

  return (
    <div style={styles.pageBackground}>
      <div style={styles.card}>
        <h2 style={styles.header}>Create Account</h2>
        
        {/* Success Message Display */}
        {isSuccess && (
          <div style={styles.successBox}>
            🎉 Form submitted successfully!
          </div>
        )}

        <form onSubmit={handleSubmit}>
          
          {/* Name Input Block */}
          <div style={{ marginBottom: '20px' }}>
            <label style={styles.label}>Full Name</label>
            <input
              type="text"
              name="name"
              placeholder="e.g. John Doe"
              value={formData.name}
              onChange={handleChange}
              style={styles.input(errors.name)}
            />
            {errors.name && <span style={styles.errorText}>{errors.name}</span>}
          </div>

          {/* Email Input Block */}
          <div style={{ marginBottom: '25px' }}>
            <label style={styles.label}>Email Address</label>
            <input
              type="text"
              name="email"
              placeholder="e.g. john@example.com"
              value={formData.email}
              onChange={handleChange}
              style={styles.input(errors.email)}
            />
            {errors.email && <span style={styles.errorText}>{errors.email}</span>}
          </div>

          {/* SUBMIT Button */}
          <button type="submit" style={styles.button}>
            SUBMIT
          </button>
          
        </form>
      </div>
    </div>
  );
}

export default App;