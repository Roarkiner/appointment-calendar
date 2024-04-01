import React from 'react';
import AuthLink from '../../molecules/header/AuthLink';
import { disconnectUser } from '../../../services/AuthService';
import HomeLink from '../../molecules/header/HomeLink';
import { useAuth } from '../../../contexts/AuthContext';

const Header: React.FC = () => {
    const { setIsAuthenticated, setIsAdmin } = useAuth();

    return (
        <header className='d-flex justify-content-between align-items-center py-3 px-5' style={{backgroundColor: '#f8f9fa' }}>
            <HomeLink />
            <AuthLink onLogout={() => { disconnectUser(setIsAuthenticated, setIsAdmin) }}/>
        </header>
    );
};

export default Header;