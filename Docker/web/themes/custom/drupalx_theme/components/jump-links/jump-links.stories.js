import JumpLinksTemplate from './jump-links.twig';
import './jump-links.scss';


export default {
  title: 'Navigation/Jump Links',
  argTypes: {
    nav_items: { control: 'object' }
  }
};

export const JumpLinks = JumpLinksTemplate.bind({});
JumpLinks.args = {
  jump_links: [
    {
      text: 'Section 1',
      url: '#section1',
      icon: '<span class="material-icons">trending_flat</span>'
    },
    {
      text: 'Section 2',
      url: '#section2',
      icon: '<span class="material-icons">trending_flat</span>'
    },
    {
      text: 'Section 3',
      url: '#section3',
      icon: '<span class="material-icons">trending_flat</span>'
    },
    {
      text: 'Section 4',
      url: '#section4',
      icon: '<span class="material-icons">trending_flat</span>'
    }
  ]
};
