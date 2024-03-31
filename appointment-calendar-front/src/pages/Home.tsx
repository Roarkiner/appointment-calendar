import "../assets/style/home.css"

import React, { useState } from "react";
import WeeklyCalendar from "../components/organisms/calendar/WeeklyCalendar";
import Header from "../components/organisms/shared/Header";
import ScheduleAppointmentModal from "../components/organisms/calendar/ScheduleAppointmentModal";

const Home: React.FC = () => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [triggerReload, setTriggerReload] = useState(false);

    function openModal() {
        setIsModalOpen(true);
    }

    function closeModal() {
        setIsModalOpen(false);
    }

    function onAppointmentCreated() {
        setTriggerReload(true);
    }

    return (
        <>
            <ScheduleAppointmentModal onAppointmentCreated={onAppointmentCreated} isModalOpen={isModalOpen} closeModal={closeModal} />
            <Header />
            <main className="home-container p-3 pe-5">
                <WeeklyCalendar onModalButtonClicked={openModal} triggerReload={triggerReload} setTriggerReload={setTriggerReload} />
            </main>
        </>
    );
};

export default Home;