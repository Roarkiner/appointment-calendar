import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { isConnectedUserAdmin, isUserAuthenticated } from '../services/AuthService';

interface AuthContextType {
    isAuthenticated: boolean;
    setIsAuthenticated: (value: boolean) => void;
    isAdmin: boolean;
    setIsAdmin: (value: boolean) => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = (): AuthContextType => {
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
    const [isAdmin, setIsAdmin] = useState<boolean>(false);

    useEffect(() => {
        try {
            const result = isUserAuthenticated();
            setIsAuthenticated(result);
            if (result) {
                setIsAdmin(isConnectedUserAdmin());
            }
        } catch (error) {
            console.error('Error checking authentication status:', error);
        }
    }, []);

    const value = { isAuthenticated, setIsAuthenticated, isAdmin, setIsAdmin };

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};