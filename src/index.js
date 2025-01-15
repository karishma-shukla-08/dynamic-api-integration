import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { useState, useEffect } from '@wordpress/element';
import axios from 'axios';

const Edit = () => {
  const blockProps = useBlockProps();
  const [data, setData] = useState([]);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
      const fetchApiData = async () => {
        const apiUrl = `${window.location.origin}/wp-json/dai/v1/fetch-data`;

          setLoading(true);
          setError(null);

          try {
              // Make a request to the custom REST API endpoint
              const response = await axios.post(apiUrl);
              
              console.log('API Response Data:', JSON.stringify(response, null, 2));
              if (response.data) {
                  setData(response.data);
              } else {
                  setError('No data returned from the API.');
              }
          } catch (err) {
              setError('Failed to fetch API data.');
              console.error(err);
          } finally {
              setLoading(false);
          }
      };

      fetchApiData();
  }, []);

  return (
      <div {...blockProps}>
          <h3>Dynamic API Data</h3>
          {loading && <p>Loading...</p>}
          {error ? (
              <p style={{ color: 'red' }}>{error}</p>
          ) : (
              <ul>
                  {data.map((item, index) => (
                      <li key={index}>{item.name || JSON.stringify(item)}</li>
                  ))}
              </ul>
          )}
      </div>
  );
};

registerBlockType('dynamic-api-integration/block', {
    title: 'Dynamic API Integration',
    category: 'widgets',
    icon: 'database',
    supports: {
        html: false,
    },
    edit: Edit,
    save: () => null, // This block is dynamic, so no frontend save logic is needed
});
