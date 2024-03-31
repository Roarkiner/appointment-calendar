import React from "react";
import ButtonAtom from '../../atoms/ButtonAtom';

interface FormSwitcherProps {
    selectedForm: "login" | "register";
    onSwitchForm: (form: "login" | "register") => void;
}

const FormSwitcher: React.FC<FormSwitcherProps> = ({ selectedForm, onSwitchForm }) => (
    <div className="form-switch-auth">
        <ButtonAtom className={selectedForm === "login" ? "active" : ""} onClick={() => onSwitchForm("login")}>
            Se connecter
        </ButtonAtom>
        <span className="separator"></span>
        <ButtonAtom className={selectedForm === "register" ? "active" : ""} onClick={() => onSwitchForm("register")}>
            Créer son compte
        </ButtonAtom>
    </div>
);

export default FormSwitcher;