import axios from 'axios'
export default axios.create({
	baseURL: '',
	timeout: 6000,
	crossDomain: true
})