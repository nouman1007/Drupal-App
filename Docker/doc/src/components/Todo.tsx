import React from 'react';
import './Todo.component.css';

interface TodoProps {
  message: string;
}

const Todo: React.FC<TodoProps> = ({ message }) => {
  return (
    <div className="todo-container">
      <strong>⚠️ {message ? message : "Additional documentation and/or revisions coming soon."} ⚠️</strong>
    </div>
  );
};

export default Todo;
