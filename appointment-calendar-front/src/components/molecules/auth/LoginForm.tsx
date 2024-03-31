import React from 'react';
import InputAtom from '../../atoms/shared/InputAtom';
import ButtonAtom from '../../atoms/shared/ButtonAtom';

interface LoginFormProps {
    email: string;
    setEmail: (value: string) => void;
    password: string;
    setPassword: (value: string) => void;
    isLoading: boolean;
    handleLogin: (e: React.FormEvent) => void;
}

const LoginForm: React.FC<LoginFormProps> = ({ email, setEmail, password, setPassword, isLoading, handleLogin }) => (
    <>
        <div className='input-group'>
            <InputAtom
                type="email"
                className="form-control"
                id="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                label="Email"
            />
            <InputAtom
                type="password"
                className="form-control"
                id="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                label="Mot de passe"
            />
            <ButtonAtom disabled={isLoading} className="btn btn-primary" onClick={handleLogin}>
                Me connecter
            </ButtonAtom>
        </div>
    </>
);

export default LoginForm;