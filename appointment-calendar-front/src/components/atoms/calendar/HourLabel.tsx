import React from 'react';

interface HourLabelAtomProps {
    hour: number;
    bottomPosition: number;
}

const HourLabelAtom: React.FC<HourLabelAtomProps> = ({ hour, bottomPosition }) => {
    return <div className="hour-label" style={{position: "absolute", bottom: bottomPosition}}>{hour}:00</div>;
};
  
export default HourLabelAtom;