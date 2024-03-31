import "./assets/style/app.css";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import 'react-toastify/dist/ReactToastify.css';

import Auth from "./pages/Auth";
import { useRoutes } from "react-router-dom";
import { AuthProvider } from './contexts/AuthContext';
import React from "react";

const AppRoutes: React.FC = () => {
    const routing = useRoutes([
        { path: "/login", element: <Auth /> },
    ]);
    
    return <>{routing}</>;
}

const App: React.FC = () => {
    return (
        <AuthProvider>
            <AppRoutes />
        </AuthProvider>
    );
};

export default App;
