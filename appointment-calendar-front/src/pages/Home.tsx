import "../assets/style/home.css"

import React from "react";
import WeeklyCalendar from "../components/organisms/calendar/WeeklyCalendar";
import Header from "../components/organisms/shared/Header";

const Home: React.FC = () => {
    return (
        <>
            <Header />
            <main className="home-container p-3 pe-5">
                <WeeklyCalendar />
            </main>
        </>
    );
};

export default Home;