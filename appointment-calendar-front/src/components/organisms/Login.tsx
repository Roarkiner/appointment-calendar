import React, { useEffect, useState } from "react";
import { loginUser } from "../../services/AuthService";
import { AxiosError } from "axios";
import { displayCustomToastError, displayDefaultToastError } from "../../services/ToastHelper";
import { useLocation } from "react-router-dom";
import LoginForm from '../molecules/Auth/LoginForm';

const Login: React.FC = () => {
    const location = useLocation();
    const queryParams = new URLSearchParams(location.search);
    const displayError: boolean = Boolean(queryParams.get("error")) || false;
    const redirectUrl: string = queryParams.get("redirectUrl") ?? "/";
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        if (displayError) {
            displayCustomToastError("Veuillez vous connecter");
        }
    }, [displayError]);

    async function handleLogin(e: React.FormEvent) {
        e.preventDefault();
        setIsLoading(true);

        try {
            await loginUser(email, password);
            // window.location.href = redirectUrl;
        } catch (error) {
            if (!(error instanceof AxiosError) || (error.response?.data.message !== "Invalid credentials.")) {
                displayDefaultToastError();
            }
            setIsLoading(false);
            throw error;
        }

        setIsLoading(false);
    };

    return (
        <>
            <h2>Se connecter</h2>
            <LoginForm 
                email={email} 
                setEmail={setEmail} 
                password={password} 
                setPassword={setPassword} 
                isLoading={isLoading} 
                handleLogin={handleLogin} 
            />
        </>
    );
};

export default Login;