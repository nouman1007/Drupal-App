// src/components/Video.tsx
import React from 'react';
import './Video.component.css';

interface VideoProps {
  url: string;
  label?: string;
  width?: string;
  height?: string;
}

const Video: React.FC<VideoProps> = ({ url, label, width = '560', height = '315' }) => {
  let embedUrl = '';
  let videoType = '';

  // Auto-detect video type
  if (url.includes('youtube.com') || url.includes('youtu.be')) {
    videoType = 'youtube';
    const urlObj = new URL(url);
    const videoId = urlObj.searchParams.get('v') || url.split('/').pop();
    const timeParam = urlObj.searchParams.get('t');
    embedUrl = `https://www.youtube.com/embed/${videoId}${timeParam ? `?start=${timeParam}` : ''}`;
  } else if (url.includes('vimeo.com')) {
    videoType = 'vimeo';
    const videoId = url.split('/').pop();
    embedUrl = `https://player.vimeo.com/video/${videoId}`;
  } else {
    return <p>Unsupported video type.</p>;
  }

  return (
    <div className="video-container">
      {label && <div className="video-label">{label}</div>}
      <div className="video-wrapper">
        <iframe
          src={embedUrl}
          className="video-frame"
          allowFullScreen
          title={`Video embed - ${videoType}`}
        />
      </div>
    </div>
  );
};

export default Video;
