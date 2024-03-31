import React from 'react';
import InputAtom from '../../atoms/InputAtom';
import ButtonAtom from '../../atoms/ButtonAtom';

interface RegistrationFormProps {
    email: string;
    setEmail: (value: string) => void;
    handleEmailBlur: () => void;
    password: string;
    setPassword: (value: string) => void;
    handlePasswordBlur: () => void;
    confirmPassword: string;
    setConfirmPassword: (value: string) => void;
    handleConfirmPasswordBlur: () => void;
    lastname: string;
    setLastname: (value: string) => void;
    handleLastnameBlur: () => void;
    firstname: string;
    setFirstname: (value: string) => void;
    handleFirstnameBlur: () => void;
    errors: { email: boolean; password: boolean; confirmPassword: boolean, lastname: boolean; firstname: boolean; };
    isLoading: boolean;
    handleRegister: (e: React.FormEvent) => void;
}

const RegistrationForm: React.FC<RegistrationFormProps> = ({
    email,
    setEmail,
    handleEmailBlur,
    password,
    setPassword,
    handlePasswordBlur,
    confirmPassword,
    setConfirmPassword,
    lastname,
    setLastname,
    handleLastnameBlur,
    firstname,
    setFirstname,
    handleFirstnameBlur,
    handleConfirmPasswordBlur,
    errors,
    isLoading,
    handleRegister,
}) => (
    <>
        <InputAtom
            type="email"
            className="form-control"
            id="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            onBlur={handleEmailBlur}
            label="Email"
        />
        {errors.email && <p className="error">Adresse email invalide</p>}

        <InputAtom
            type="password"
            className="form-control"
            id="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            onBlur={handlePasswordBlur}
            label="Mot de passe"
        />
        {errors.password && <p className="error">Le mot de passe doit être long de 8 caractères</p>}

        <InputAtom
            type="password"
            className="form-control"
            id="confirmPassword"
            value={confirmPassword}
            onChange={(e) => setConfirmPassword(e.target.value)}
            onBlur={handleConfirmPasswordBlur}
            label="Confirmation du mot de passe"
        />
        {errors.confirmPassword && <p className="error">La confirmation est différente du mot de passe.</p>}

        <InputAtom
            type="text"
            className="form-control"
            id="lastname"
            value={lastname}
            onChange={(e) => setLastname(e.target.value)}
            label="Nom"
            onBlur={handleLastnameBlur}
        />
        {errors.lastname && <p className="error">Nom requis</p>}

        <InputAtom
            type="text"
            className="form-control"
            id="firstname"
            value={firstname}
            onChange={(e) => setFirstname(e.target.value)}
            label="Prénom"
            onBlur={handleFirstnameBlur}
        />
        {errors.firstname && <p className="error">Prénom requis</p>}

        <ButtonAtom disabled={isLoading} className="btn btn-primary" onClick={handleRegister}>
            Créer mon compte
        </ButtonAtom>
    </>
);

export default RegistrationForm;