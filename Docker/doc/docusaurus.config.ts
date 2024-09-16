import {themes as prismThemes} from 'prism-react-renderer';
import type {Config} from '@docusaurus/types';
import type * as Preset from '@docusaurus/preset-classic';

const config: Config = {
  title: 'AmeriCorps',
  tagline: '',
  favicon: 'img/favicon.ico',

  // Set the production url of your site here
  url: 'https://americorps.gov',
  // Set the /<baseUrl>/ pathname under which your site is served
  // For GitHub pages deployment, it is often '/<projectName>/'
  baseUrl: '/',

  // GitHub pages deployment config.
  // If you aren't using GitHub pages, you don't need these.
  organizationName: 'facebook', // Usually your GitHub org/user name.
  projectName: 'docusaurus', // Usually your repo name.

  onBrokenLinks: 'throw',
  onBrokenMarkdownLinks: 'warn',

  // Even if you don't use internationalization, you can use this field to set
  // useful metadata like html lang. For example, if your site is Chinese, you
  // may want to replace "en" with "zh-Hans".
  i18n: {
    defaultLocale: 'en',
    locales: ['en'],
  },

  presets: [
    [
      'classic',
      {
        docs: {
          sidebarPath: './sidebars.ts',
          // Please change this to your repo.
          // Remove this to remove the "edit this page" links.
          editUrl:
            'https://github.com/skytech-americorps/LearningCampusLMS',
        },
        theme: {
          customCss: './src/css/custom.css',
        },
      } satisfies Preset.Options,
    ],
  ],

  themeConfig: {
    // Replace with your project's social card
    image: 'img/docusaurus-social-card.jpg',
    navbar: {
      title: '',
      logo: {
        alt: 'Americorps Logo',
        src: 'img/logo.svg',
      },
      items: [
        {
          type: 'docSidebar',
          sidebarId: 'developSidebar',
          position: 'left',
          label: 'Develop',
        },
        {
          type: 'docSidebar',
          sidebarId: 'administerSidebar',
          position: 'left',
          label: 'Administer',
        },
        {
          type: 'docSidebar',
          sidebarId: 'testSidebar',
          position: 'left',
          label: 'Test',
        },
        {
          type: 'docSidebar',
          sidebarId: 'secureSidebar',
          position: 'left',
          label: 'Secure',
        },
        {
          type: 'docSidebar',
          sidebarId: 'buildSidebar',
          position: 'left',
          label: 'Build',
        },
        {
          type: 'docSidebar',
          sidebarId: 'deploySidebar',
          position: 'left',
          label: 'Deploy',
        },
        {
          href: 'https://github.com/skytech-americorps/LearningCampusLMS',
          label: 'GitHub',
          position: 'right',
        },
        {
          href: 'https://learning-campus-lms.ddev.site',
          label: 'Sandbox',
          position: 'right',
        },
        {
          href: 'https://devdrupal10-3-1-defea3dwgaa8ceaf.eastus-01.azurewebsites.net',
          label: 'Dev',
          position: 'right',
        },
        {
          href: 'https://uat1americorpswebapp.azurewebsites.net',
          label: 'UAT',
          position: 'right',
        },
        {
          href: 'https://americorps.gov',
          label: 'Prod',
          position: 'right',
        }
      ],
    },
    footer: {
      style: 'dark',
      links: [
        {
          title: 'Docs',
          items: [
            {
              label: 'Develop',
              to: '/docs/develop',
            },
            {
              label: 'Build',
              to: '/docs/build',
            },
            {
              label: 'Deploy',
              to: '/docs/deploy',
            },
          ],
        },
      ],
      copyright: `Copyright © ${new Date().getFullYear()} Americorps.`,
    },
    prism: {
      theme: prismThemes.github,
      darkTheme: prismThemes.dracula,
    },
  } satisfies Preset.ThemeConfig,

  markdown: {
    mermaid: true,
  },
  themes: ['@docusaurus/theme-mermaid'],
};

export default config;
