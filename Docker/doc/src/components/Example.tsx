import React from 'react';
import styles from './Example.module.css';

interface ExampleProps {
  code: string;
  label?: string;
}

const Example: React.FC<ExampleProps> = ({ code, label }) => {
  return (
    <div className={styles.example}>
      {label && <div className={styles.label}>{label}</div>}
      <pre>
        <code>{code}</code>
      </pre>
    </div>
  );
};

export default Example;
