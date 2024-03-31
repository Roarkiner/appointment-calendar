import React from "react";
import { Link } from "react-router-dom";

interface LinkAtomProps {
    to: string;
    className?: string;
    children: React.ReactNode;
}

const LinkAtom: React.FC<LinkAtomProps> = ({ to, className, children }) => (
    <Link to={to} className={className}>
        {children}
    </Link>
);

export default LinkAtom;