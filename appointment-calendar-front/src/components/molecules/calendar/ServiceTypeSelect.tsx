import { convertISODurationToReadableFormat } from "../../../services/DateHelper";
import ServiceTypeSelectOptionAtom from "../../atoms/calendar/ServiceTypeSelectOptionAtom";

interface ServiceType {
    id: number;
    name: string;
    duration: string;
}

interface ServiceTypeSelectProps {
serviceTypes: ServiceType[];
selectedServiceType: string;
setSelectedServiceType: (value: string) => void;
}
  
const ServiceTypeSelect: React.FC<ServiceTypeSelectProps> = ({ serviceTypes, selectedServiceType, setSelectedServiceType }) => {

    return (
        <select
            className="mb-4"
            value={selectedServiceType}
            onChange={(e) => setSelectedServiceType(e.target.value)}
        >
            <option value="">Sélectionnez le type de prestation</option>
            {serviceTypes.map((serviceType) => (
                <ServiceTypeSelectOptionAtom
                    key={serviceType.id}
                    id={serviceType.id}
                    name={serviceType.name}
                    duration={convertISODurationToReadableFormat(serviceType.duration)}/>
            ))}
        </select>
    );
};

export default ServiceTypeSelect;