import "../../../assets/style/modal.css";

import React, { useEffect, useState } from "react";
import "react-datepicker/dist/react-datepicker.css";
import { useAuth } from "../../../contexts/AuthContext";
import Modal from "react-modal";
import AddAppointmentForm from "../../molecules/calendar/AddAppointmentForm";
import { getAllServiceTypes } from "../../../services/ServiceTypeService";
import { toast } from "react-toastify";
import { saveAppointment } from "../../../services/AppointmentService";
import { addISODurationToDate, formatDate } from "../../../services/DateHelper";
import { getCurrentUserInfo } from "../../../services/UserService";
import { AxiosError } from "axios";

interface ScheduleAppointmentModalProps {
    isModalOpen: boolean;
    closeModal: () => void;
    onAppointmentCreated: () => void;
    triggerReloadServiceTypes: boolean;
    setTriggerReloadServiceTypes: (value: boolean) => void;
}

Modal.setAppElement('#root');

const ScheduleAppointmentModal: React.FC<ScheduleAppointmentModalProps> = ({ isModalOpen, closeModal, onAppointmentCreated, triggerReloadServiceTypes, setTriggerReloadServiceTypes }) => {
    const { isAuthenticated } = useAuth();
    const [selectedDate, setSelectedDate] = useState<Date | null>(new Date());
    const [selectedServiceType, setSelectedServiceType] = useState("");
    const [serviceTypeList, setServiceTypeList] = useState<ServiceType[]>([]);
    const [isLoading, setIsLoading] = useState(false);

    const loadServiceTypes = async (): Promise<void> => {
        const fetchedServiceTypes = await getAllServiceTypes();
        setServiceTypeList(fetchedServiceTypes);
    }
    
    useEffect(() => {
        if (triggerReloadServiceTypes) {
            loadServiceTypes();
            setTriggerReloadServiceTypes(false);
        }
    }, [triggerReloadServiceTypes, setTriggerReloadServiceTypes]);

    const trySaveAppointment = async (): Promise<void> => {
        setIsLoading(true);
        const selectedServiceTypeObj = serviceTypeList.find(s => s.id == parseInt(selectedServiceType));

        if (selectedServiceType == undefined) {
            toast.error("Une erreur s'est produite", {
                position:'bottom-right',
                autoClose: 3000,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: false
            });
            setIsLoading(false);
            closeModal();
            return;
        }

        if (selectedDate == null) {
            toast.error("Veuillez sélectionner une date", {
                position:'bottom-right',
                autoClose: 3000,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: false
            });
            setIsLoading(false);
            return;
        }

        const currentUserInfo = await getCurrentUserInfo();
        
        try {

            const appointmentSaveModel: AppointmentSaveModel = {
                start_date: formatDate(selectedDate!),
                end_date: formatDate(addISODurationToDate(selectedDate!, selectedServiceTypeObj?.duration!)),
                service_type_id: selectedServiceTypeObj?.id! ?? 0,
                user_id: currentUserInfo.id
            }

            const saveAppointmentResponse = await saveAppointment(appointmentSaveModel);
            
            setIsLoading(false);
    
            if (saveAppointmentResponse.status == 201) {
                toast.success("Le rendez-vous a été créé avec succès", {
                    position:'bottom-right',
                    autoClose: 3000,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: false
                });
            }
            
            onAppointmentCreated();
            closeModal();
        } catch (e: any) {
            if (e instanceof AxiosError ) {
                if (Array.isArray(e.response?.data.errors)) {
                    e.response?.data.errors.forEach((errorMessage: string) => {
                        toast.error(errorMessage, {
                            position:'bottom-right',
                            autoClose: 3000,
                            closeOnClick: true,
                            pauseOnHover: true,
                            draggable: false
                        });
                    });
                } else {
                    toast.error(e.response?.data.errors, {
                        position:'bottom-right',
                        autoClose: 3000,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: false
                    });
                }
            } else {
                toast.error(e.message, {
                    position:'bottom-right',
                    autoClose: 3000,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: false
                });
            }
        } finally {
            setIsLoading(false);
        }
    }

    if (!isAuthenticated) {
        return null;
    }

    if (isLoading) return (
        <Modal
            isOpen={isModalOpen}
            onRequestClose={closeModal}
            contentLabel="Prendre un rendez-vous"
            className="Modal"
            overlayClassName="Overlay"
        >
            <div className="mt-5 text-center">Chargement...</div>
        </Modal>
    );

    return (
        <Modal
            isOpen={isModalOpen}
            onRequestClose={closeModal}
            contentLabel="Prendre un rendez-vous"
            className="Modal"
            overlayClassName="Overlay"
        >
            <AddAppointmentForm selectedDate={selectedDate}
                setSelectedDate={setSelectedDate}
                selectedServiceType={selectedServiceType}
                setSelectedServiceType={setSelectedServiceType}
                serviceTypeList={serviceTypeList}
                onFormCompletion={trySaveAppointment} />
      </Modal>
  );
};

export default ScheduleAppointmentModal;
