import "../assets/style/home.css"

import React, { useState } from "react";
import WeeklyCalendar from "../components/organisms/calendar/WeeklyCalendar";
import Header from "../components/organisms/shared/Header";
import ScheduleAppointmentModal from "../components/organisms/calendar/ScheduleAppointmentModal";
import AddSlotModal from "../components/organisms/calendar/AddSlotModal";
import AddServiceTypeModal from "../components/organisms/calendar/AddServiceTypeModal";

const Home: React.FC = () => {
    const [isAppointmentModalOpen, setIsAppointmentModalOpen] = useState(false);
    const [isSlotModalOpen, setIsSlotModalOpen] = useState(false);
    const [isServiceTypeModalOpen, setIsServiceTypeModalOpen] = useState(false);
    const [triggerReloadCalendar, setTriggerReloadCalendar] = useState(false);
    const [triggerReloadServiceTypes, setTriggerReloadServiceTypes] = useState(true);
   
    function reloadCalendar() {
        setTriggerReloadCalendar(true);
    }

    function reloadServiceTypes() {
        setTriggerReloadServiceTypes(true);
    }

    function openAppointmentModal() {
        setIsAppointmentModalOpen(true);
    }

    function closeAppointmentModal() {
        setIsAppointmentModalOpen(false);
    }

    function openSlotModal() {
        setIsSlotModalOpen(true);
    }

    function closeSlotModal() {
        setIsSlotModalOpen(false);
    }
    
    function openServiceTypeModal() {
        setIsServiceTypeModalOpen(true);
    }

    function closeServiceTypeModal() {
        setIsServiceTypeModalOpen(false);
    }

    return (
        <>
            <ScheduleAppointmentModal 
                onAppointmentCreated={reloadCalendar} 
                isModalOpen={isAppointmentModalOpen} 
                closeModal={closeAppointmentModal}
                triggerReloadServiceTypes={triggerReloadServiceTypes}
                setTriggerReloadServiceTypes={setTriggerReloadServiceTypes} />
            <AddSlotModal 
                onSlotCreated={reloadCalendar} 
                isModalOpen={isSlotModalOpen} 
                closeModal={closeSlotModal} />
            <AddServiceTypeModal 
                isModalOpen={isServiceTypeModalOpen} 
                closeModal={closeServiceTypeModal} 
                onServiceTypeCreated={reloadServiceTypes}/>
            <Header />
            <main className="home-container p-3 pe-5">
                <WeeklyCalendar 
                    onAppointmentModalButtonClicked={openAppointmentModal} 
                    onSlotModalButtonClicked={openSlotModal}
                    onServiceTypeModalButtonClicked={openServiceTypeModal}
                    triggerReloadCalendar={triggerReloadCalendar} 
                    setTriggerReloadCalendar={setTriggerReloadCalendar} />
            </main>
        </>
    );
};

export default Home;