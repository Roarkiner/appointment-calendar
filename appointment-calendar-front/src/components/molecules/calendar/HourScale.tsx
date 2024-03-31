import React from 'react';
import HourLabelAtom from '../../atoms/calendar/HourLabel';

const HourScale: React.FC<{ quarterHourHeight: number }> = ({quarterHourHeight}) => {
    const hours = [];
    for (let hour = 7; hour <= 19; hour++) {
      hours.push(
        <HourLabelAtom key={hour} hour={hour} bottomPosition={(19 - hour) * quarterHourHeight * 4 + 5}/>
      );
    }
  
    return <>{hours}</>;
  };
  
  export default HourScale;