import React from 'react';
import './Warning.component.css';

interface WarningProps {
  message: string;
}

const Warning: React.FC<WarningProps> = ({ message }) => {
  return (
    <div className="warning-container">
      <strong>Warning:</strong> {message}
    </div>
  );
};

export default Warning;
