import React from 'react';

interface DrupalProps {
  path: string;
  label?: string;
}

const Drupal: React.FC<DrupalProps> = ({ path, label }) => {
  // Hardcoded base URL
  const baseUrl = 'https://learning-campus-lms.ddev.site';

  // Construct the full URL
  const fullUrl = `${baseUrl}/${path.replace(/^\/+/, '')}`;

  // Extract the last part of the path
  const lastPartOfPath = path.split('/').pop() || path;

  // Replace dashes and underscores with spaces and capitalize each word
  const formatText = (text: string) => {
    return text
      .replace(/[-_]/g, ' ') // Replace dashes and underscores with spaces
      .replace(/\b\w/g, (char) => char.toUpperCase()); // Capitalize the first letter of each word
  };

  // Display label or formatted last part of the path
  const displayText = label || formatText(lastPartOfPath);

  return (
    <a href={fullUrl} target="_blank" rel="noopener noreferrer">
      {displayText}
    </a>
  );
};

export default Drupal;
