import React from 'react';
import AuthLink from '../../molecules/header/AuthLink';
import { disconnectUser } from '../../../services/AuthService';
import HomeLink from '../../molecules/header/HomeLink';


const Header: React.FC = () => (
  <header className='d-flex justify-content-between align-items-center py-3 px-5' style={{backgroundColor: '#f8f9fa' }}>
    <HomeLink />
    <AuthLink onLogout={disconnectUser}/>
  </header>
);

export default Header;