import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { isUserAuthenticated } from '../services/AuthService';

interface AuthContextType {
    isAuthenticated: boolean;
    setIsAuthenticated: (value: boolean) => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};

interface AuthProviderProps {
    children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
    const [isAuthenticated, setIsAuthenticated] = useState<boolean>(false);

    useEffect(() => {
        try {
            const result = isUserAuthenticated();
            setIsAuthenticated(result);
        } catch (error) {
            console.error('Error checking authentication status:', error);
        }
    }, []);

    const value = { isAuthenticated, setIsAuthenticated };

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};