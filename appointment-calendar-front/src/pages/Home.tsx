import "../assets/style/home.css"

import React from "react";
import WeeklyCalendar from "../components/organisms/calendar/WeeklyCalendar";

const Home: React.FC = () => {
    return (
        <div className="home-container p-3 pe-5">
            <WeeklyCalendar />
        </div>
    );
};

export default Home;