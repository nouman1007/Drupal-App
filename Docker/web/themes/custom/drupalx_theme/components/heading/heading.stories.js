import './heading.scss';
import HeadingTemplate from './heading.twig';

export default {
  title: 'General/Heading'
};

export const Heading = HeadingTemplate.bind({});
Heading.args = {
  heading: {
    title: 'Title Lorem Ipsum Dolor',
    subtitle: 'Subtitle Lorem Ipsum Dolor',
    level: '2',
    html_id: 'heading-id',
    modifier: 'display-3',
    url: ''
  }
};
