import React from 'react';
import './Critical.component.css';

interface CriticalProps {
  message: string;
}

const Critical: React.FC<CriticalProps> = ({ message }) => {
  return (
    <div className="critical-container">
      <strong>Critical:</strong> {message}
    </div>
  );
};

export default Critical;
