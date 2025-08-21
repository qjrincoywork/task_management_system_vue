import React, { useState } from 'react';
import Login from './components/Login';
import Register from './components/Register';

const App: React.FC = () => {
    const [isLogin, setIsLogin] = useState(true);

    const toggleForm = () => {
        setIsLogin(!isLogin);
    };

    return (
        <div>
            <h1>{isLogin ? 'Login' : 'Register'}</h1>
            {isLogin ? <Login /> : <Register />}
            <button onClick={toggleForm}>
                {isLogin ? 'Switch to Register' : 'Switch to Login'}
            </button>
        </div>
    );
};

export default App;