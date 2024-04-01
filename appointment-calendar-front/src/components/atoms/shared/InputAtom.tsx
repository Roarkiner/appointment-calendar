import React from 'react';

interface InputAtomProps extends React.InputHTMLAttributes<HTMLInputElement> {
    type: string;
    className: string;
    id: string;
    value: string | number;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    label: string;
    onBlur?: (e: React.FocusEvent<HTMLInputElement>) => void;
}

const InputAtom: React.FC<InputAtomProps> = ({ type, className, id, value, onChange, label, onBlur, ...props }) => (
    <div className="form-group">
        <label htmlFor={id}>{label}</label>
        <input
            type={type}
            className={className}
            id={id}
            value={value}
            onChange={onChange}
            onBlur={onBlur}
            {...props}
        />
    </div>
);

export default InputAtom;