import React from 'react';

interface LogoAtomProps {
    alt: string;
    src: string;
    height: string;
}

const LogoAtom: React.FC<LogoAtomProps> = ({ alt, src, height }) => (
    <img src={src} alt={alt} style={{ height }} />
);

export default LogoAtom;