import React from 'react';

interface ButtonAtomProps {
    disabled?: boolean;
    className: string;
    onClick: (e: React.FormEvent) => void;
    children: React.ReactNode;
}

const ButtonAtom: React.FC<ButtonAtomProps> = ({ disabled, className, onClick, children }) => (
    <button disabled={disabled} className={className} onClick={onClick}>
        {children}
    </button>
);

export default ButtonAtom;