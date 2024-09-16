import React from 'react';
import './Note.component.css';

interface NoteProps {
  message: string;
}

const Note: React.FC<NoteProps> = ({ message }) => {
  return (
    <div className="note-container">
      <strong>Note:</strong> {message}
    </div>
  );
};

export default Note;
