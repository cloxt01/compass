// db/mysql.js
import mysql from 'mysql2/promise';

export const db = await mysql.createPool({
  host: '127.0.0.1',
  user: 'root',
  password: '',
  database: 'compass',
  waitForConnections: true,
  connectionLimit: 5,
  queueLimit: 0
});
