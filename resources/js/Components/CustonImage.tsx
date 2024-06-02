import React from 'react'

interface Props {
    src: string;
    width?: number;
    height?: number;
    className?: string;
    alt?: string;
}

const CustonImage: React.FC<Props> = ({ src, width, height, className, alt }) => {
    return (<img src={src} alt={alt} width={width} height={height} className={className} />)
}

export default CustonImage
