import React from 'react';
import LinkAtom from '../../atoms/shared/LinkAtom';
import { useAuth } from '../../../contexts/AuthContext';
import ButtonAtom from '../../atoms/shared/ButtonAtom';

interface AuthLinkProps {
    onLogout: () => void;
}

const AuthLink: React.FC<AuthLinkProps> = ({ onLogout }) => {
  const { isAuthenticated } = useAuth();

    return (
        <div>
        {isAuthenticated ? (
            <ButtonAtom className="logout" onClick={onLogout}>Déconnexion</ButtonAtom>
        ) : (
            <LinkAtom to="/login">Connexion</LinkAtom>
        )}
        </div>
    );
};

export default AuthLink;