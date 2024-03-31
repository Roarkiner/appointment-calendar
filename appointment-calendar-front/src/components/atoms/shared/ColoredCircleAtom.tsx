import React from "react";

interface ColoredCircleAtomProps {
    width: number;
    height: number;
    backgroundColor: string;
}

const ColoredCircleAtom: React.FC<ColoredCircleAtomProps> = ({ width, height, backgroundColor }) => {
    return (<div className="me-2" style={{width: `${width}px`, height: `${height}px`, borderRadius: '50%', backgroundColor}}></div>)
}

export default ColoredCircleAtom;